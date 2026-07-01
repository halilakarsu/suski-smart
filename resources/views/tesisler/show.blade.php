@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }
    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }
    .page-hero { background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%); position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px; color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); }
    .page-hero::before { content: ''; position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%); top: -150px; right: -100px; border-radius: 50%; opacity: 0.5; filter: blur(60px); animation: pulseSlow 10s infinite alternate; pointer-events: none; }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.6; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em; background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem; }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card { background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px; box-shadow: var(--shadow-elevated); margin-bottom: 30px; }
    .detail-label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
    .detail-value { font-size: 0.95rem; font-weight: 600; color: var(--text-slate-900); }
    .detail-value.null { color: #cbd5e1; font-weight: 400; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
    .info-item { padding: 16px; background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 10px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
    .status-active { background: #f0fdf4; color: #16a34a; }
    .status-passive { background: #fef2f2; color: #dc2626; }
    .btn-pro { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important; }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(139, 92, 246, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }
    .section-title { font-size: 0.85rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px; }
    .section-title i { color: #8b5cf6; }

    .map-wrapper { position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0; }
    .map-wrapper:hover #tesisMap { pointer-events: auto; }
    #tesisMap { pointer-events: none; width: 100%; height: 420px; z-index: 1; }
    #tesisMap.leaflet-container { border-radius: 0; }
    .map-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: #fff; }
    .map-header-left { display: flex; align-items: center; gap: 12px; }
    .map-header-left i { font-size: 1.1rem; color: #a78bfa; }
    .map-header-left span { font-weight: 700; font-size: 0.85rem; letter-spacing: 0.02em; }
    .map-coords { display: flex; align-items: center; gap: 16px; }
    .map-coords .coord { font-size: 0.7rem; font-weight: 500; color: #94a3b8; font-family: 'SF Mono', 'Monaco', monospace; }
    .map-coords .coord strong { color: #c4b5fd; }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Tesis Detayı</h1>
                <p class="hero-subtitle">{{ $tesis->ilce }} / {{ $tesis->mahalle }}</p>
            </div>
            <a href="{{ route('tesis-bilgi-sistemi.tesisler') }}" class="btn-pro" style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);color:#fff;">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <span style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Durum</span>
                    <div style="margin-top:4px;">
                        @if($tesis->durum == 'aktif')
                        <span class="status-badge status-active"><i class="fas fa-check-circle"></i> Aktif</span>
                        @else
                        <span class="status-badge status-passive"><i class="fas fa-minus-circle"></i> Pasif</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('tesis-bilgi-sistemi.tesisler.edit', $tesis->id) }}" class="btn-pro btn-primary-pro" style="padding:10px 20px;font-size:0.85rem;">
                        <i class="fas fa-pen"></i> Düzenle
                    </a>
                </div>
            </div>

            <div class="section-title"><i class="fas fa-map-marker-alt"></i> Konum Bilgileri</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="detail-label">İlçe</div>
                    <div class="detail-value">{{ $tesis->ilce }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Mahalle / Köy</div>
                    <div class="detail-value">{{ $tesis->mahalle ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Sokak / Mevki</div>
                    <div class="detail-value {{ $tesis->sokak ? '' : 'null' }}">{{ $tesis->sokak ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Kuyu No</div>
                    <div class="detail-value {{ $tesis->kuyu_no ? '' : 'null' }}">{{ $tesis->kuyu_no ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">CBS X (Enlem)</div>
                    <div class="detail-value {{ $tesis->cbs_x ? '' : 'null' }}">{{ $tesis->cbs_x ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">CBS Y (Boylam)</div>
                    <div class="detail-value {{ $tesis->cbs_y ? '' : 'null' }}">{{ $tesis->cbs_y ?? '—' }}</div>
                </div>
            </div>

            <div class="section-title" style="margin-top:30px;"><i class="fas fa-file-invoice"></i> Abone & Fatura Bilgileri</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="detail-label">Abone No</div>
                    <div class="detail-value {{ $tesis->abone_no ? '' : 'null' }}">{{ $tesis->abone_no ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Abone Adı</div>
                    <div class="detail-value {{ $tesis->abone ? '' : 'null' }}">{{ $tesis->abone->UNVAN ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Abone Tipi</div>
                    <div class="detail-value {{ $tesis->abone_tipi ? '' : 'null' }}">{{ $tesis->abone_tipi ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Sayaç No</div>
                    <div class="detail-value {{ $tesis->sayac_no ? '' : 'null' }}">{{ $tesis->sayac_no ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Toplam Fatura Tutarı</div>
                    <div class="detail-value {{ $tesis->toplam_fatura_tutari ? '' : 'null' }}">{{ $tesis->toplam_fatura_tutari ? number_format($tesis->toplam_fatura_tutari, 2) . ' TL' : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Gelir / Gider</div>
                    <div class="detail-value {{ $tesis->gelir ? '' : 'null' }}">{{ $tesis->gelir ? number_format($tesis->gelir, 2) . ' TL' : '—' }} / {{ $tesis->gider ? number_format($tesis->gider, 2) . ' TL' : '—' }}</div>
                </div>
            </div>

            <div class="section-title" style="margin-top:30px;"><i class="fas fa-calendar-alt"></i> Tarih Bilgileri</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="detail-label">Tesis Kurulma Tarihi</div>
                    <div class="detail-value {{ $tesis->tesis_kurulma_tarihi ? '' : 'null' }}">{{ $tesis->tesis_kurulma_tarihi ? $tesis->tesis_kurulma_tarihi->format('d.m.Y') : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Hibe Tarihi</div>
                    <div class="detail-value {{ $tesis->hibe_tarihi ? '' : 'null' }}">{{ $tesis->hibe_tarihi ? $tesis->hibe_tarihi->format('d.m.Y') : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Abone Tarihi</div>
                    <div class="detail-value {{ $tesis->abone_tarihi ? '' : 'null' }}">{{ $tesis->abone_tarihi ? $tesis->abone_tarihi->format('d.m.Y') : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Demontaj Tarihi</div>
                    <div class="detail-value {{ $tesis->demontaj_tarihi ? '' : 'null' }}">{{ $tesis->demontaj_tarihi ? $tesis->demontaj_tarihi->format('d.m.Y') : '—' }}</div>
                </div>
            </div>

            <div class="section-title" style="margin-top:30px;"><i class="fas fa-microchip"></i> Teknik Bilgiler</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="detail-label">Trafo Gücü</div>
                    <div class="detail-value {{ $tesis->trafo_gucu ? '' : 'null' }}">{{ $tesis->trafo_gucu ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Trafo Seri No</div>
                    <div class="detail-value {{ $tesis->trafo_seri_no ? '' : 'null' }}">{{ $tesis->trafo_seri_no ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Trafo CBS</div>
                    <div class="detail-value {{ $tesis->trafo_cbs ? '' : 'null' }}">{{ $tesis->trafo_cbs ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">ENH Durumu</div>
                    <div class="detail-value {{ $tesis->enh_durumu ? '' : 'null' }}">{{ $tesis->enh_durumu ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Keşif Durumu</div>
                    <div class="detail-value {{ $tesis->kesif_durumu ? '' : 'null' }}">{{ $tesis->kesif_durumu ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="detail-label">Demontaj Malzemeler</div>
                    <div class="detail-value {{ $tesis->demontaj_yapilan_malzemeler ? '' : 'null' }}">{{ $tesis->demontaj_yapilan_malzemeler ?? '—' }}</div>
                </div>
            </div>

            @if($tesis->abone_iptali_yazildi_mi || $tesis->abone_iptal_edildi_mi || $tesis->kacak_elektrik_kullanimi_var_mi || $tesis->kacak_borcu_var_mi)
            <div class="section-title" style="margin-top:30px;"><i class="fas fa-exclamation-triangle"></i> Uyarı Bilgileri</div>
            <div class="info-grid">
                @if($tesis->abone_iptali_yazildi_mi)
                <div class="info-item">
                    <div class="detail-label">Abone İptali Yazıldı</div>
                    <div class="detail-value" style="color:#dc2626;">Evet</div>
                </div>
                @endif
                @if($tesis->abone_iptal_edildi_mi)
                <div class="info-item">
                    <div class="detail-label">Abone İptal Edildi</div>
                    <div class="detail-value" style="color:#dc2626;">Evet</div>
                </div>
                @endif
                @if($tesis->kacak_elektrik_kullanimi_var_mi)
                <div class="info-item">
                    <div class="detail-label">Kaçak Elektrik</div>
                    <div class="detail-value" style="color:#dc2626;">Var</div>
                </div>
                @endif
                @if($tesis->kacak_borcu_var_mi)
                <div class="info-item">
                    <div class="detail-label">Kaçak Borcu</div>
                    <div class="detail-value" style="color:#dc2626;">Var</div>
                </div>
                @endif
            </div>
            @endif

            @if($tesis->cbs_x && $tesis->cbs_y)
            <div style="margin-top:30px;">
                <div class="section-title" style="margin-bottom:16px;"><i class="fas fa-globe"></i> Konum Haritası</div>
                <div class="map-wrapper">
                    <div class="map-header">
                        <div class="map-header-left">
                            <i class="fas fa-satellite"></i>
                            <span>{{ $tesis->ilce }} / {{ $tesis->mahalle }}</span>
                        </div>
                        <div class="map-coords">
                            <span class="coord" id="coordDisplay">Dönüştürülüyor...</span>
                        </div>
                    </div>
                    <div id="mapContainer" style="width:100%;height:380px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:0.9rem;color:#94a3b8;">
                        <i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i> CBS koordinatları dönüştürülüyor...
                    </div>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.12.0/proj4.js"></script>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
            (function() {
                var rawX = parseFloat('{{ $tesis->cbs_x }}');
                var rawY = parseFloat('{{ $tesis->cbs_y }}');
                if (!rawX || !rawY || isNaN(rawX) || isNaN(rawY)) return;

                var container = document.getElementById('mapContainer');
                var coordDisplay = document.getElementById('coordDisplay');

                function isValidLL(lat, lng) {
                    return lat > 35 && lat < 43 && lng > 25 && lng < 45;
                }

                function convertCBS(x, y) {
                    if (isValidLL(y, x)) return { lat: y, lng: x };
                    if (isValidLL(x, y)) return { lat: x, lng: y };

                    var pairs = [[x, y], [y, x]];
                    var epsgCodes = ['EPSG:23037','EPSG:32637','EPSG:23036','EPSG:32636','EPSG:5255','EPSG:5254'];
                    var scales = [1, 10, 100, 1000, 10000, 100000];

                    for (var p = 0; p < pairs.length; p++) {
                        for (var s = 0; s < scales.length; s++) {
                            var sx = pairs[p][0] / scales[s];
                            var sy = pairs[p][1] / scales[s];
                            for (var e = 0; e < epsgCodes.length; e++) {
                                try {
                                    var r = proj4(epsgCodes[e], 'EPSG:4326', [sx, sy]);
                                    if (r && isValidLL(r[1], r[0])) return { lat: r[1], lng: r[0] };
                                } catch(_) {}
                            }
                        }
                    }
                    return null;
                }

                var coords = convertCBS(rawX, rawY);

                if (coords) {
                    coordDisplay.innerHTML = '<strong>K</strong> ' + coords.lat.toFixed(6) + ' &nbsp; <strong>D</strong> ' + coords.lng.toFixed(6);
                    container.innerHTML = '<div id="tesisMap" style="width:100%;height:420px;"></div>';
                    var map = L.map('tesisMap', { zoomControl: false, scrollWheelZoom: false }).setView([coords.lat, coords.lng], 13);
                    L.control.zoom({ position: 'bottomright' }).addTo(map);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 20, attribution: '© OpenStreetMap' }).addTo(map);
                    L.marker([coords.lat, coords.lng]).addTo(map)
                        .bindPopup('<strong>{{ $tesis->mahalle }}</strong><br>{{ $tesis->ilce }}<br>Kuyu: {{ $tesis->kuyu_no ?? '&mdash;' }}')
                        .openPopup();
                    setTimeout(function() { map.invalidateSize(); }, 100);
                    map.on('mouseover', function() { map.scrollWheelZoom.enable(); });
                    map.on('mouseout', function() { map.scrollWheelZoom.disable(); });
                } else {
                    coordDisplay.innerHTML = 'Dönüştürülemedi';
                    container.innerHTML = '<div style="text-align:center;padding:60px 20px;color:#94a3b8;"><i class="fas fa-map-marked-alt mb-3" style="font-size:2.5rem;display:block;"></i><p style="font-weight:600;">CBS koordinatları haritaya dönüştürülemedi.</p><small style="color:#cbd5e1;">X=' + rawX + ', Y=' + rawY + '</small></div>';
                }
            })();
            </script>
            @endif

            <div style="margin-top:30px;padding-top:20px;border-top:1px solid #e2e8f0;display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('tesis-bilgi-sistemi.tesisler') }}" class="btn-pro btn-outline-pro">
                    <i class="fas fa-arrow-left"></i> Tesis Listesi
                </a>
                <a href="{{ route('tesis-bilgi-sistemi.tesisler.edit', $tesis->id) }}" class="btn-pro btn-primary-pro">
                    <i class="fas fa-pen"></i> Düzenle
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
