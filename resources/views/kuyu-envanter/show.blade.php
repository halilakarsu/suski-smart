@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #064e3b 100%);
        position: relative; padding: 4rem 2rem 8rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
    }
    .page-hero::before {
        content: ''; position: absolute; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
        top: -150px; left: -100px; border-radius: 50%; filter: blur(60px); pointer-events: none;
    }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-size: 2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #fff, #6ee7b7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.3rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 0.95rem; }
    
    .main-container { width: 100%; max-width: 1200px; margin: -4rem auto 0; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 24px; padding: 32px; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); margin-bottom: 24px;
    }
    
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px; }
    .detail-item { display: flex; flex-direction: column; gap: 6px; }
    .detail-label { font-size: 0.82rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .detail-value { font-size: 1.05rem; font-weight: 600; color: #0f172a; word-break: break-word; }
    .detail-value.empty { color: #94a3b8; font-style: italic; font-weight: 400; font-size: 0.95rem; }
    
    .section-header {
        font-size: 1.1rem; font-weight: 700; color: #0f172a; padding: 10px 0; margin: 32px 0 20px;
        border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 10px;
    }
    .section-header:first-child { margin-top: 0; }
    .section-header i { color: #10b981; font-size: 1.2rem; }
    
    .btn {
        padding: 10px 22px; border-radius: 12px; border: none; font-weight: 600; cursor: pointer;
        font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12); }
    .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
    
    .badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
    .badge-aktif { background: #d1fae5; color: #059669; }
    .badge-pasif { background: #f1f5f9; color: #64748b; }
    
    .aciklama-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; font-size: 0.95rem; color: #334155; line-height: 1.6; white-space: pre-wrap; }
    
    .footer-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9; }
</style>

<div class="page-hero">
    <div class="hero-container">
        <div class="hero-title-group">
            <h1>🪣 Kuyu Detayı — #{{ $kuyu->kuyu_no ?? $kuyu->id }}</h1>
            <p class="hero-subtitle">Kuyu Envanteri / Kayıt Detayı</p>
        </div>
        <div>
            <span class="badge badge-{{ $kuyu->durum }}">{{ $kuyu->durum_label }}</span>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="glass-card">
        
        {{-- Temel Bilgiler --}}
        <div class="section-header"><i class="fas fa-info-circle"></i> Temel Bilgiler</div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Kuyu Numarası</span>
                <span class="detail-value">{{ $kuyu->kuyu_no ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Abone No / Tesisat No</span>
                <span class="detail-value">{{ $kuyu->abone_no ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">İlçe</span>
                <span class="detail-value">{{ $kuyu->ilce ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Durum</span>
                <span class="detail-value" style="text-transform: capitalize;">{{ $kuyu->durum }}</span>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <span class="detail-label">Adres</span>
                <span class="detail-value {!! empty($kuyu->adres) ? 'empty' : '' !!}">{{ $kuyu->adres ?? 'Adres bilgisi girilmemiş.' }}</span>
            </div>
        </div>

        {{-- CBS Koordinatları --}}
        <div class="section-header"><i class="fas fa-map-marker-alt"></i> CBS Koordinatları</div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">CBS X Koordinatı</span>
                <span class="detail-value">{{ $kuyu->cbs_x ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">CBS Y Koordinatı</span>
                <span class="detail-value">{{ $kuyu->cbs_y ?? '-' }}</span>
            </div>
        </div>

        @if($kuyu->cbs_x && $kuyu->cbs_y)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <div style="margin-top:16px;">
            <div class="map-wrapper" style="position:relative;border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.06);border:1px solid #e2e8f0;">
                <div class="map-header" style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%);color:#fff;border-radius:0;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <i class="fas fa-satellite" style="color:#a78bfa;"></i>
                        <span style="font-weight:700;font-size:0.85rem;">{{ $kuyu->ilce }} / {{ $kuyu->adres }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <span id="coordDisplay" style="font-size:0.7rem;font-weight:500;color:#94a3b8;font-family:monospace;">Dönüştürülüyor...</span>
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
            var rawX = parseFloat('{{ $kuyu->cbs_x }}');
            var rawY = parseFloat('{{ $kuyu->cbs_y }}');
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
                coordDisplay.innerHTML = '<strong style="color:#c4b5fd;">K</strong> ' + coords.lat.toFixed(6) + ' &nbsp; <strong style="color:#c4b5fd;">D</strong> ' + coords.lng.toFixed(6);
                container.innerHTML = '<div id="kuyuMap" style="width:100%;height:420px;" class="leaflet-container"></div>';
                var map = L.map('kuyuMap', { zoomControl: false, scrollWheelZoom: false }).setView([coords.lat, coords.lng], 13);
                L.control.zoom({ position: 'bottomright' }).addTo(map);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 20, attribution: '© OpenStreetMap' }).addTo(map);
                L.marker([coords.lat, coords.lng]).addTo(map)
                    .bindPopup('<strong>Kuyu #{{ $kuyu->kuyu_no }}</strong><br>{{ $kuyu->ilce }}<br>{{ $kuyu->adres }}')
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
        <style>
            .map-wrapper:hover #kuyuMap { pointer-events: auto; }
            #kuyuMap { pointer-events: none; width: 100%; height: 420px; z-index: 1; }
            #kuyuMap.leaflet-container { border-radius: 0; }
        </style>
        @endif

        {{-- Teknik Bilgiler --}}
        <div class="section-header"><i class="fas fa-cogs"></i> Teknik Bilgiler</div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Demontaj Derinliği</span>
                <span class="detail-value">{{ $kuyu->demontaj_derinligi ? number_format($kuyu->demontaj_derinligi, 2) . ' m' : '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Montaj Derinliği</span>
                <span class="detail-value">{{ $kuyu->montaj_derinligi ? number_format($kuyu->montaj_derinligi, 2) . ' m' : '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Debi</span>
                <span class="detail-value">{{ $kuyu->debi ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Boru Tipi</span>
                <span class="detail-value">{{ $kuyu->boru_tipi ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kablo</span>
                <span class="detail-value">{{ $kuyu->kablo ?? '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Depo Bilgisi</span>
                <span class="detail-value">{{ $kuyu->depo_bilgisi ?? '-' }}</span>
            </div>
            <div class="detail-item" style="grid-column: span 2;">
                <span class="detail-label">Motor</span>
                <span class="detail-value">{{ $kuyu->motor ?? '-' }}</span>
            </div>
            <div class="detail-item" style="grid-column: span 2;">
                <span class="detail-label">Pompa</span>
                <span class="detail-value">{{ $kuyu->pompa ?? '-' }}</span>
            </div>
        </div>

        {{-- Tarihler --}}
        <div class="section-header"><i class="fas fa-calendar-alt"></i> Tarihler</div>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Oluşturulma Tarihi</span>
                <span class="detail-value">{{ $kuyu->olusturulma_tarihi ? $kuyu->olusturulma_tarihi->format('d.m.Y H:i') : '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Güncellenme Tarihi</span>
                <span class="detail-value">{{ $kuyu->guncellenme_tarihi ? $kuyu->guncellenme_tarihi->format('d.m.Y H:i') : '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Sisteme Kayıt Tarihi</span>
                <span class="detail-value">{{ $kuyu->created_at ? $kuyu->created_at->format('d.m.Y H:i') : '-' }}</span>
            </div>
        </div>

        {{-- Açıklama --}}
        <div class="section-header"><i class="fas fa-comment-dots"></i> Açıklama / Notlar</div>
        @if(!empty($kuyu->aciklama))
            <div class="aciklama-box">{{ $kuyu->aciklama }}</div>
        @else
            <span class="detail-value empty">Açıklama veya not bulunmuyor.</span>
        @endif

        <div class="footer-actions">
            <a href="{{ route('kuyu-envanteri.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
            @can('manage_kuyu_envanteri')
            <a href="{{ route('kuyu-envanteri.edit', $kuyu) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Düzenle
            </a>
            @endcan
        </div>

    </div>
</div>
@endsection
