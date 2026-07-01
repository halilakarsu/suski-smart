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
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; }
    .hero-title-group h1 {
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.3rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); margin-bottom: 30px;
    }
    .filter-form { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 20px; }
    .filter-form select, .filter-form input { padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.9rem; min-width: 160px; }
    .filter-form button { padding: 10px 24px; border-radius: 12px; border: none; background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; font-weight: 600; cursor: pointer; }
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
    .table-pro th { text-align: left; padding: 12px 16px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-pro td { padding: 14px 16px; background: rgba(255,255,255,0.7); border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .table-pro tr:first-child td { border-radius: 16px 16px 0 0; }
    .table-pro tr:last-child td { border-radius: 0 0 16px 16px; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .badge-aktif { background: #d1fae5; color: #059669; }
    .badge-pasif { background: #fef2f2; color: #dc2626; }
    .pagination-wrap { display: flex; justify-content: center; margin-top: 20px; }
</style>
<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1>Tesisler</h1>
                <p class="hero-subtitle">Tüm tesis listesi ve detayları</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <form class="filter-form" method="GET">
                <select name="ilce">
                    <option value="">Tüm İlçeler</option>
                    @foreach($ilceler as $i)
                    <option value="{{ $i }}" {{ request('ilce') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endforeach
                </select>
                <select name="durum">
                    <option value="">Tüm Durumlar</option>
                    <option value="aktif" {{ request('durum') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="pasif" {{ request('durum') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                </select>
                <input type="text" name="arama" placeholder="Mahalle / Kuyu No / Abone No" value="{{ request('arama') }}">
                <button type="submit">Filtrele</button>
                @if(request()->anyFilled(['ilce', 'durum', 'arama']))
                <a href="{{ route('tesis-bilgi-sistemi.tesisler') }}" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;color:#64748b;text-decoration:none;">Temizle</a>
                @endif
            </form>

            <table class="table-pro">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>İlçe</th>
                        <th>Mahalle</th>
                        <th>Kuyu No</th>
                        <th>Abone No</th>
                        <th>Durum</th>
                        <th>Trafo</th>
                        <th>Demontaj</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tesisler as $t)
                    <tr>
                        <td>{{ $t->sira_no }}</td>
                        <td><strong>{{ $t->ilce }}</strong></td>
                        <td>{{ $t->mahalle }}</td>
                        <td>{{ $t->kuyu_no ?? '-' }}</td>
                        <td>{{ $t->abone_no ?? '-' }}</td>
                        <td><span class="badge badge-{{ $t->durum }}">{{ $t->durum == 'aktif' ? 'Aktif' : 'Pasif' }}</span></td>
                        <td>{{ $t->trafo_gucu ?? '-' }}</td>
                        <td>{{ $t->demontaj_tarihi ? $t->demontaj_tarihi->format('d.m.Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $tesisler->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
