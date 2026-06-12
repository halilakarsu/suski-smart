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
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Abone Detayı: {{ $abone->ABONE_TESIS_NO }}</h1>
                <p class="hero-subtitle">Abone profili, sayaç geçmişi ve güncelleme tarihçesi.</p>
            </div>
            <a href="{{ route('aboneler.index') }}" class="btn-pro btn-outline-pro">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="main-container">
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

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('tuketimChart');
    if (!ctx) return;

    var labels = @json($sonYilTuketim->pluck('donem'));
    var values = @json($sonYilTuketim->pluck('fatura_edilecek_toplam_tuketim_kwh'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tüketim (kWh)',
                data: values,
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 2,
                borderRadius: 6,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { weight: '700', size: 13 },
                    bodyFont: { weight: '600', size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ctx.parsed.y.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' kWh';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { weight: '600', size: 11 },
                        callback: function(v) { return v.toLocaleString('tr-TR') + ' kWh'; }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: '600', size: 10 } }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
