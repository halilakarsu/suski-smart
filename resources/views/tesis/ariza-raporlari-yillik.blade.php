@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
    }
    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -20px; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, transparent 70%);
        top: -200px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none; z-index: 1;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(1.2); opacity: 0.9; } }
    .hero-container { position: relative; z-index: 10; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); margin-bottom: 30px;
    }
    .donem-card {
        background: var(--surface-glass); backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 22px; padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.2,0.8,0.2,1);
        display: flex; flex-direction: column; text-decoration: none !important; cursor: default;
    }
    .donem-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.12); background: #fff; }
    .donem-value { font-size: 1.6rem; font-weight: 800; color: var(--text-slate-900); z-index: 2; margin-top: 5px; line-height: 1.2; }
    .donem-stat-row { display: flex; justify-content: space-between; align-items: center; padding-top: 8px; margin-top: 8px; }
    .donem-stat-label { font-size: 0.65rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .donem-stat-val { font-size: 0.8rem; color: #334155; font-weight: 700; }
    .ay-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: linear-gradient(135deg, #a78bfa, #7c3aed); margin-bottom: 12px; }
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
    .table-pro th { text-align: left; padding: 12px 16px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border: none; }
    .table-pro td { padding: 14px 16px; background: rgba(255,255,255,0.7); border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .table-pro tr:hover td { background: #f8fafc; }
    .table-pro tr td:first-child { border-radius: 16px 0 0 16px; }
    .table-pro tr td:last-child { border-radius: 0 16px 16px 0; }
    .badge-ariza { background: #fef2f2; color: #dc2626; padding: 4px 12px; border-radius: 99px; font-weight: 700; font-size: 0.85rem; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1>İstatistiksel Veriler</h1>
                <p class="hero-subtitle">Excel'den alınan yıllara göre arıza sayıları</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card" style="padding: 15px 25px; margin-bottom: 20px;">
            <div style="display: flex; gap: 8px; justify-content: flex-start; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 5px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                @foreach($yillar as $yil)
                    @php $aktif = ($yil == $selectedYil); @endphp
                    <a href="{{ route('tesis-bilgi-sistemi.ariza-raporlari.yillik', ['yil' => $yil]) }}"
                       style="padding: 8px 20px; border-radius: 99px; font-weight: 700; font-size: 0.95rem; white-space: nowrap; flex-shrink: 0; text-decoration: none !important; transition: all 0.3s; display: inline-block;
                              color: {{ $aktif ? '#fff' : '#64748b' }}; background: {{ $aktif ? 'linear-gradient(135deg, #8b5cf6, #7c3aed)' : '#f1f5f9' }};
                              box-shadow: {{ $aktif ? '0 10px 20px -5px rgba(139, 92, 246, 0.4)' : 'none' }};">
                        {{ $yil }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="glass-card" style="padding:14px 16px;margin-bottom:20px;border:2px solid #ede9fe;height:100%;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#7c3aed;">
                            <i class="fas fa-calendar-alt" style="margin-right:5px;"></i> {{ $selectedYil }} Aylık Dağılım
                        </div>
                        <div style="font-size:.7rem;font-weight:700;color:#94a3b8;">
                            <i class="fas fa-wrench"></i> <span style="color:#7c3aed;">{{ number_format($yilToplam[$selectedYil] ?? 0) }}</span>
                        </div>
                    </div>
                    <div class="row g-1">
                        @foreach(range(1, 12) as $ayNo)
                            @php
                                $aySayi = 0;
                                foreach ($ilceBazli as $ib) {
                                    foreach ($ib['aylar'] as $a) {
                                        if ($a['ay'] == $ayNo) $aySayi += $a[$selectedYil] ?? 0;
                                    }
                                }
                            @endphp
                            <div class="col-6 mb-1">
                                <div class="donem-card" onclick="showAyDetay({{ $selectedYil }}, {{ $ayNo }})" style="cursor:pointer;padding:12px;border-radius:16px;min-height:auto;">
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                        <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;background:linear-gradient(135deg,#a78bfa,#7c3aed);flex-shrink:0;"><i class="fas fa-calendar-day"></i></div>
                                        <div style="font-size:.8rem;font-weight:700;color:var(--text-slate-500);">{{ $ayIsimleri[$ayNo] }}</div>
                                    </div>
                                    <div style="font-size:1.1rem;font-weight:800;color:#dc2626;">{{ number_format($aySayi) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card" style="padding:18px 22px;margin-bottom:20px;height:100%;">
                    <h4 style="font-weight:700; margin-bottom:14px; display:flex; align-items:center; gap:8px; font-size:.85rem;">
                        <i class="fas fa-map-marker-alt" style="color:#7c3aed;"></i> İlçe Bazında Dağılım
                    </h4>
                    <table class="table-pro" style="font-size:.8rem;">
                        <thead>
                            <tr>
                                <th style="padding:6px 8px;font-size:.65rem;">İlçe</th>
                                <th style="padding:6px 8px;font-size:.65rem;text-align:right;">Sayı</th>
                                <th style="padding:6px 8px;font-size:.65rem;text-align:right;">Oran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $ilceToplam = max(1, $yilToplam[$selectedYil] ?? 0); @endphp
                            @forelse($ilceBazli as $ib)
                            <tr>
                                <td style="padding:6px 8px;"><strong style="font-size:.8rem;">{{ $ib['ilce'] }}</strong></td>
                                <td style="padding:6px 8px;text-align:right;font-weight:700;font-size:.8rem;">{{ number_format($ib['yil_toplam'][$selectedYil] ?? 0) }}</td>
                                <td style="padding:6px 8px;text-align:right;">
                                    <span class="badge-ariza" style="font-size:.65rem;">{{ number_format((($ib['yil_toplam'][$selectedYil] ?? 0) / $ilceToplam) * 100, 1) }}%</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="text-align:center;padding:30px;color:#94a3b8;">Veri bulunamadı.</td></tr>
                            @endforelse
                            <tr style="border-top:2px solid #e2e8f0; background:#f8fafc;">
                                <td style="padding:6px 8px;font-weight:800;font-size:.8rem;">Toplam</td>
                                <td style="padding:6px 8px;text-align:right;font-weight:800;color:#7c3aed;font-size:.8rem;">{{ number_format($ilceToplam) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row" style="display:flex; flex-wrap:wrap;">
            <div class="col-md-6" style="display:flex;">
                <div class="glass-card" style="padding:18px 22px; width:100%;">
                    <h5 style="font-weight:700; margin-bottom:14px; display:flex; align-items:center; gap:8px; font-size:.85rem;">
                        <i class="fas fa-chart-bar" style="color:#7c3aed;"></i> Aylık Dağılım
                    </h5>
                    <div style="position:relative; height:200px;">
                        <canvas id="arizaChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6" style="display:flex;">
                <div class="glass-card" style="padding:18px 22px; width:100%;">
                    <h5 style="font-weight:700; margin-bottom:14px; display:flex; align-items:center; gap:8px; font-size:.85rem;">
                        <i class="fas fa-map-marker-alt" style="color:#7c3aed;"></i> İlçe Bazında
                    </h5>
                    <div style="position:relative; height:200px;">
                        <canvas id="ilceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ay Detay Modal -->
<div id="ayModal" style="position:fixed;inset:0;background:rgba(15,23,42,0.85);z-index:9990;display:none;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(10px);">
    <div style="background:#f8fafc;border-radius:32px;width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 80px rgba(0,0,0,0.35);overflow:hidden;">
        <div style="padding:25px 35px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;justify-content:space-between;align-items:center;color:#fff;">
            <h2 id="ayModalTitle" style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;">-</h2>
            <button onclick="document.getElementById('ayModal').style.display='none'" style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;font-size:1.2rem;"><i class="fas fa-times"></i></button>
        </div>
        <div style="flex:1;overflow-y:auto;padding:35px;">
            <table class="table-pro" id="ayModalTable">
                <thead><tr><th>İlçe</th><th style="text-align:right;">Arıza Sayısı</th></tr></thead>
                <tbody id="ayModalBody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
const ayIsimleri = ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];

function showAyDetay(yil, ay) {
    document.getElementById('ayModalTitle').textContent = ayIsimleri[ay] + ' ' + yil + ' - İlçe Detayı';
    const tbody = document.getElementById('ayModalBody');
    tbody.innerHTML = '<tr><td colspan="2" style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</td></tr>';
    document.getElementById('ayModal').style.display = 'flex';

    fetch('{{ route("tesis-bilgi-sistemi.ariza-raporlari.yillik.detay", ["yil" => "YIL", "ay" => "AY"]) }}'.replace('YIL', yil).replace('AY', ay), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        let html = '';
        let toplam = 0;
        data.forEach(r => { toplam += r.toplam; });
        data.forEach(r => {
            html += `<tr><td><strong>${r.ilce}</strong></td><td style="text-align:right;font-weight:700;">${Number(r.toplam).toLocaleString('tr-TR')}</td></tr>`;
        });
        html += `<tr style="border-top:2px solid #e2e8f0;background:#f8fafc;"><td style="font-weight:800;">Toplam</td><td style="text-align:right;font-weight:800;color:#7c3aed;">${toplam.toLocaleString('tr-TR')}</td></tr>`;
        tbody.innerHTML = html;
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align:center;padding:40px;color:#ef4444;">Veri yüklenemedi.</td></tr>';
    });
}
</script>

@push('scripts')
<script>
$(function() {
    if (typeof Chart === 'undefined') return;

    var chartOptions = {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Arıza Sayısı',
                data: @json($chartValues),
                backgroundColor: '#7c3aed',
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: {
                backgroundColor: '#ffffff',
                titleFontColor: '#1e293b',
                bodyFontColor: '#475569',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                titleFontStyle: 'bold',
                bodyFontStyle: 'bold',
                titleFontSize: 11,
                bodyFontSize: 10,
                xPadding: 8,
                yPadding: 8,
                cornerRadius: 6,
                callbacks: {
                    label: function(item) {
                        return Number(item.yLabel).toLocaleString('tr-TR') + ' Arıza';
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontColor: '#64748b',
                        fontSize: 10,
                        padding: 6,
                        callback: function(v) { return Number(v).toLocaleString('tr-TR'); }
                    },
                    gridLines: { color: '#e2e8f0', drawBorder: false, zeroLineColor: '#e2e8f0' }
                }],
                xAxes: [{
                    gridLines: { display: false },
                    ticks: { fontColor: '#64748b', fontSize: 9 }
                }]
            },
            animation: { duration: 600, easing: 'easeOutQuart' }
        }
    };

    var ctx = document.getElementById('arizaChart');
    if (ctx) new Chart(ctx, chartOptions);

    var ilceCtx = document.getElementById('ilceChart');
    if (ilceCtx) {
        new Chart(ilceCtx, {
            type: 'bar',
            data: {
                labels: @json($ilceChartLabels),
                datasets: [{
                    label: 'Arıza Sayısı',
                    data: @json($ilceChartValues),
                    backgroundColor: '#7c3aed',
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    backgroundColor: '#ffffff',
                    titleFontColor: '#1e293b',
                    bodyFontColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    titleFontStyle: 'bold',
                    bodyFontStyle: 'bold',
                    titleFontSize: 11,
                    bodyFontSize: 10,
                    xPadding: 8,
                    yPadding: 8,
                    cornerRadius: 6,
                    callbacks: {
                        label: function(item) {
                            return Number(item.yLabel).toLocaleString('tr-TR') + ' Arıza';
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#64748b',
                            fontSize: 10,
                            padding: 6,
                            callback: function(v) { return Number(v).toLocaleString('tr-TR'); }
                        },
                        gridLines: { color: '#e2e8f0', drawBorder: false, zeroLineColor: '#e2e8f0' }
                    }],
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: '#64748b', fontSize: 9 }
                    }]
                },
                animation: { duration: 600, easing: 'easeOutQuart' }
            }
        });
    }
});
</script>
@endpush
@endsection