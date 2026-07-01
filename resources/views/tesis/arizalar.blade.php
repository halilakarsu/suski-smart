@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
    }
    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
        top: -200px; left: -150px; border-radius: 50%; opacity: 0.6; filter: blur(60px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.3rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; }
    .hero-action-link { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; color: #fff; font-weight: 700; font-size: 0.85rem; text-decoration: none !important; transition: all 0.3s; }
    .hero-action-link:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); color: #fff; }
    .hero-action-link i { font-size: 1rem; opacity: 0.8; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); margin-bottom: 30px;
    }
    .filter-form { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 20px; }
    .filter-form select, .filter-form input { padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.9rem; min-width: 160px; }
    .filter-form button { padding: 10px 24px; border-radius: 12px; border: none; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; font-weight: 600; cursor: pointer; }
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
    .table-pro th { text-align: left; padding: 12px 16px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-pro td { padding: 14px 16px; background: rgba(255,255,255,0.7); border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .table-pro tr:first-child td { border-radius: 16px 16px 0 0; }
    .table-pro tr:last-child td { border-radius: 0 0 16px 16px; }
    .badge-ariza { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #fef3c7; color: #92400e; }
    .badge-durum { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
    .badge-durum.kayit { background: #dbeafe; color: #1e40af; }
    .badge-durum.devam { background: #fef3c7; color: #92400e; }
    .badge-durum.giderildi { background: #d1fae5; color: #065f46; }
    .badge-durum.bekleme { background: #f3e8ff; color: #6b21a8; }
    .action-group { display: flex; gap: 6px; justify-content: center; }
    .action-group .btn { padding: 6px 12px; border-radius: 8px; font-size: 0.78rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .action-group .btn-edit { background: #fef3c7; color: #92400e; }
    .action-group .btn-edit:hover { background: #fde68a; }
    .action-group .btn-delete { background: #fee2e2; color: #991b1b; }
    .action-group .btn-delete:hover { background: #fecaca; }
    .pagination-wrap { display: flex; justify-content: center; margin-top: 20px; }
</style>
<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1>Arıza Kayıtları</h1>
                <p class="hero-subtitle">Tüm arıza kayıtları ve filtreleme</p>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <a href="{{ route('tesis-bilgi-sistemi.arizalar.create') }}" style="display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border:none;border-radius:16px;color:#fff;font-weight:800;font-size:0.95rem;text-decoration:none !important;box-shadow:0 10px 30px -5px rgba(139,92,246,0.4);transition:all 0.3s;">
                    <i class="fas fa-plus-circle" style="font-size:1.1rem;"></i> Yeni Arıza Kaydı
                </a>
                <a href="{{ route('tesis-bilgi-sistemi.ariza-turleri') }}" class="hero-action-link">
                    <i class="fas fa-tags"></i> Arıza Türleri
                </a>
                <a href="{{ route('tesis-bilgi-sistemi.ekip') }}" class="hero-action-link">
                    <i class="fas fa-users"></i> Ekipler
                </a>
            </div>
        </div>
    </div>

    <div class="main-container">
        {{-- Durum İstatistik Kartları --}}
        <div class="stat-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px;">
            <a href="{{ route('tesis-bilgi-sistemi.arizalar') }}" class="stat-card" style="background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 4px 16px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;transition:all .2s;{{ !$aktifFiltre ? 'border-color:#8b5cf6;box-shadow:0 0 0 2px rgba(139,92,246,.2);' : '' }}">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#e0e7ff,#a5b4fc);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#4338ca;flex-shrink:0;">
                    <i class="fas fa-list"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($toplamAriza) }}</div>
                    <div style="font-size:.75rem;color:#64748b;font-weight:500;">Toplam</div>
                </div>
            </a>
            <a href="{{ route('tesis-bilgi-sistemi.arizalar', ['filter' => 'giderildi']) }}" class="stat-card" style="background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 4px 16px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;transition:all .2s;{{ $aktifFiltre === 'giderildi' ? 'border-color:#059669;box-shadow:0 0 0 2px rgba(5,150,105,.2);' : '' }}">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#d1fae5,#6ee7b7);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#065f46;flex-shrink:0;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($giderilenAriza) }}</div>
                    <div style="font-size:.75rem;color:#64748b;font-weight:500;">Onarılan</div>
                </div>
            </a>
            <a href="{{ route('tesis-bilgi-sistemi.arizalar', ['filter' => 'devam']) }}" class="stat-card" style="background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 4px 16px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;transition:all .2s;{{ $aktifFiltre === 'devam' ? 'border-color:#d97706;box-shadow:0 0 0 2px rgba(217,119,6,.2);' : '' }}">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fcd34d);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#92400e;flex-shrink:0;">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($devamAriza) }}</div>
                    <div style="font-size:.75rem;color:#64748b;font-weight:500;">Devam Eden</div>
                </div>
            </a>
            <a href="{{ route('tesis-bilgi-sistemi.arizalar', ['filter' => 'bekleme']) }}" class="stat-card" style="background:#fff;border-radius:18px;padding:18px 20px;box-shadow:0 4px 16px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;transition:all .2s;{{ $aktifFiltre === 'bekleme' ? 'border-color:#7c3aed;box-shadow:0 0 0 2px rgba(124,58,237,.2);' : '' }}">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#f3e8ff,#d8b4fe);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#6b21a8;flex-shrink:0;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($beklemeAriza) }}</div>
                    <div style="font-size:.75rem;color:#64748b;font-weight:500;">Beklemede</div>
                </div>
            </a>
        </div>

        <div class="glass-card">
            <form class="filter-form" method="GET">
                @if($aktifFiltre)
                <input type="hidden" name="filter" value="{{ $aktifFiltre }}">
                @endif
                <select name="ilce">
                    <option value="">Tüm İlçeler</option>
                    @foreach($ilceler as $i)
                    <option value="{{ $i }}" {{ request('ilce') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endforeach
                </select>
                <select name="ariza_turu">
                    <option value="">Tüm Arıza Türleri</option>
                    @foreach($arizaTurleri as $a)
                    <option value="{{ $a }}" {{ request('ariza_turu') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
                <input type="date" name="tarih_baslangic" value="{{ request('tarih_baslangic') }}">
                <input type="date" name="tarih_bitis" value="{{ request('tarih_bitis') }}">
                <input type="text" name="arama" placeholder="Mahalle / Ekip / Kuyu No" value="{{ request('arama') }}">
                <button type="submit">Filtrele</button>
                @if(request()->anyFilled(['filter', 'ilce', 'ariza_turu', 'tarih_baslangic', 'tarih_bitis', 'arama']))
                <a href="{{ route('tesis-bilgi-sistemi.arizalar') }}" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;color:#64748b;text-decoration:none;">Temizle</a>
                @endif
            </form>

            <table class="table-pro">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kuyu No</th>
                        <th>Arıza Türü</th>
                        <th>Durum</th>
                        <th>Ekip</th>
                        <th style="text-align:center">İşlem</th>
                    </tr>
                        </thead>
                        <tbody>
                            @forelse($arizalar as $a)
                            @php
                                $durumClass = match($a->durum) {
                                    'Arıza Kaydı Yapıldı' => 'kayit',
                                    'Devam Ediyor' => 'devam',
                                    'Arıza Giderildi' => 'giderildi',
                                    'Beklemede' => 'bekleme',
                                    default => 'kayit',
                                };
                            @endphp
                            <tr>
                                <td>{{ $a->tarih ? $a->tarih->format('d.m.Y') : '-' }}</td>
                                <td><strong>{{ $a->kuyu_no ?? '-' }}</strong></td>
                                <td><span class="badge-ariza">{{ $a->ariza_turu }}</span></td>
                                <td><span class="badge-durum {{ $durumClass }}">{{ $a->durum ?? 'Arıza Kaydı Yapıldı' }}</span></td>
                                <td>{{ $a->ekip ?? '-' }}</td>
                                <td>
                                    <div class="action-group">
                                        <button type="button" class="btn" style="padding:6px 12px;border-radius:8px;font-size:0.78rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-weight:600;background:#e0e7ff;color:#4338ca;" title="Durum Güncelle" onclick="openStatusModal({{ $a->id }}, '{{ $a->durum ?? 'Arıza Kaydı Yapıldı' }}')"><i class="fas fa-sync-alt"></i></button>
                                        <a href="{{ route('tesis-bilgi-sistemi.arizalar.edit', $a->id) }}" class="btn btn-edit" title="Düzenle"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('tesis-bilgi-sistemi.arizalar.destroy', $a->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Bu arıza kaydını silmek istediğinize emin misiniz?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-delete" title="Sil"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Kayıt bulunamadı.</td></tr>
                            @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $arizalar->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.durum-card {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 20px;
    padding: 24px;
    border: 1px solid rgba(139, 92, 246, 0.1);
}
.durum-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
}
.durum-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    box-shadow: 0 8px 20px -5px rgba(139, 92, 246, 0.3);
}
.durum-header-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}
.durum-header-sub {
    font-size: 0.78rem;
    color: #64748b;
    font-weight: 500;
}
.durum-current {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding: 12px 16px;
    background: #f1f5f9;
    border-radius: 12px;
}
.durum-current-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.durum-current-badge {
    margin-left: auto;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
}
.durum-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 8px;
}
.durum-option:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.durum-option.selected {
    border-color: #8b5cf6;
    background: #f5f3ff;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.durum-option-radio {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
}
.durum-option.selected .durum-option-radio {
    border-color: #8b5cf6;
    background: #8b5cf6;
}
.durum-option-radio::after {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #fff;
    display: none;
}
.durum-option.selected .durum-option-radio::after {
    display: block;
}
.durum-option-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.durum-option-text {
    flex: 1;
}
.durum-option-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
}
.durum-option-desc {
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 1px;
}
.durum-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e2e8f0;
}
.durum-actions .btn {
    flex: 1;
    padding: 12px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.88rem;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}
.durum-actions .btn-primary {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: #fff;
    box-shadow: 0 8px 20px -5px rgba(139, 92, 246, 0.3);
}
.durum-actions .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 30px -5px rgba(139, 92, 246, 0.4);
}
.durum-actions .btn-secondary {
    background: #f1f5f9;
    color: #64748b;
}
.durum-actions .btn-secondary:hover {
    background: #e2e8f0;
}
</style>
<script>
function openStatusModal(id, currentDurum) {
    const durumlar = [
        { value: 'Arıza Kaydı Yapıldı', icon: 'fa-clipboard-list', bg: '#dbeafe', color: '#1e40af', desc: 'Arıza yeni kaydedildi, henüz müdahale edilmedi.' },
        { value: 'Devam Ediyor', icon: 'fa-hard-hat', bg: '#fef3c7', color: '#92400e', desc: 'Ekip sahada, arıza giderme çalışmaları sürüyor.' },
        { value: 'Arıza Giderildi', icon: 'fa-check-circle', bg: '#d1fae5', color: '#065f46', desc: 'Arıza başarıyla giderildi, tesis çalışır durumda.' },
        { value: 'Beklemede', icon: 'fa-clock', bg: '#f3e8ff', color: '#6b21a8', desc: 'Yedek parça / ekip / uygun zaman bekleniyor.' }
    ];

    let selectedDurum = currentDurum;

    const html = `
        <div class="durum-card">
            <div class="durum-header">
                <div class="durum-header-icon"><i class="fas fa-sync-alt"></i></div>
                <div>
                    <div class="durum-header-title">Durum Güncelle</div>
                    <div class="durum-header-sub">Arıza kaydının durumunu değiştirin</div>
                </div>
            </div>
            <div class="durum-current">
                <i class="fas fa-flag" style="color:#94a3b8;font-size:0.9rem;"></i>
                <span class="durum-current-label">Mevcut Durum</span>
                <span class="durum-current-badge" style="background:${durumlar.find(d => d.value === currentDurum).bg};color:${durumlar.find(d => d.value === currentDurum).color};">
                    ${currentDurum}
                </span>
            </div>
            <div id="durumOptions">
                ${durumlar.map(d => `
                    <div class="durum-option ${d.value === selectedDurum ? 'selected' : ''}" data-value="${d.value}" onclick="selectDurum(this)">
                        <div class="durum-option-radio"></div>
                        <div class="durum-option-icon" style="background:${d.bg};color:${d.color};"><i class="fas ${d.icon}"></i></div>
                        <div class="durum-option-text">
                            <div class="durum-option-title">${d.value}</div>
                            <div class="durum-option-desc">${d.desc}</div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="durum-actions">
                <button class="btn btn-secondary" onclick="Swal.close()"><i class="fas fa-times"></i> İptal</button>
                <button class="btn btn-primary" id="confirmStatusBtn"><i class="fas fa-check"></i> Güncelle</button>
            </div>
        </div>
    `;

    Swal.fire({
        html: html,
        showConfirmButton: false,
        showCancelButton: false,
        background: 'transparent',
        padding: 0,
        width: 480,
        customClass: { popup: 'swal-premium' }
    });

    document.getElementById('confirmStatusBtn').addEventListener('click', function() {
        if (selectedDurum === currentDurum) {
            Swal.fire({
                icon: 'warning',
                title: 'Durum Aynı',
                text: 'Mevcut durum ile aynı. Lütfen farklı bir durum seçin.',
                confirmButtonText: 'Tamam',
                confirmButtonColor: '#8b5cf6',
                customClass: { popup: 'glass-card-sm', confirmButton: 'btn-pro btn-primary-pro' },
                buttonsStyling: false
            });
            return;
        }
        fetch('{{ url("tesis-bilgi-sistemi/arizalar") }}/' + id + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ durum: selectedDurum })
        }).then(function(r) {
            if (r.redirected) { window.location.href = r.url; }
            else { window.location.reload(); }
        });
    });
}

function selectDurum(el) {
    document.querySelectorAll('.durum-option').forEach(function(opt) {
        opt.classList.remove('selected');
    });
    el.classList.add('selected');
    window.selectedDurum = el.dataset.value;
}
</script>
@endpush
@endsection
