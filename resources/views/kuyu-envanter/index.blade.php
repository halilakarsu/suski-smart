@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #064e3b 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,.15);
    }
    .page-hero::before {
        content:''; position:absolute; width:600px; height:600px;
        background:radial-gradient(circle, rgba(16,185,129,.3) 0%, transparent 70%);
        top:-200px; left:-150px; border-radius:50%; opacity:.6; filter:blur(60px);
        animation:pulseSlow 10s infinite alternate; pointer-events:none;
    }
    @keyframes pulseSlow{0%{transform:scale(1);opacity:.4}100%{transform:scale(1.1);opacity:.7}}
    .hero-container{position:relative;z-index:10;width:100%;max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center}
    .hero-title-group h1{font-size:2.4rem;font-weight:800;letter-spacing:-.04em;
        background:linear-gradient(to right,#fff,#6ee7b7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.4rem}
    .hero-subtitle{color:#94a3b8;font-size:1rem;font-weight:500}
    .main-container{width:100%;max-width:1400px;margin:-5rem auto 0;padding:0 2rem;position:relative;z-index:20}

    /* Stat cards */
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:18px;margin-bottom:28px}
    .stat-card{background:#fff;border-radius:20px;padding:20px 22px;box-shadow:0 4px 20px rgba(0,0,0,.06);border:1px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:14px;transition:transform .2s,box-shadow .2s}
    .stat-card{position:relative;overflow:hidden}
    .stat-card.active{border-color:#10b981;box-shadow:0 0 0 2px rgba(16,185,129,.25)}
    .stat-card.active::after{content:'';position:absolute;top:0;left:0;width:3px;height:100%;background:#10b981;border-radius:3px}
    a.stat-card{text-decoration:none;color:inherit;cursor:pointer}
    a.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 30px rgba(0,0,0,.1)}
    .stat-icon{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
    .stat-icon.green{background:linear-gradient(135deg,#d1fae5,#6ee7b7);color:#065f46}
    .stat-icon.blue{background:linear-gradient(135deg,#dbeafe,#93c5fd);color:#1e40af}
    .stat-icon.red{background:linear-gradient(135deg,#fee2e2,#fca5a5);color:#991b1b}
    .stat-icon.orange{background:linear-gradient(135deg,#fef3c7,#fcd34d);color:#92400e}
    .stat-value{font-size:1.65rem;font-weight:800;color:#0f172a;line-height:1}
    .stat-label{font-size:.8rem;color:#64748b;margin-top:4px;font-weight:500}

    /* Glass card */
    .glass-card{background:rgba(255,255,255,.88);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.7);border-radius:28px;padding:28px;box-shadow:0 20px 40px -10px rgba(0,0,0,.08);margin-bottom:28px}

    /* Toolbar */
    .toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:18px}
    .toolbar input,.toolbar select{padding:9px 14px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;font-size:.88rem;outline:none;transition:border-color .2s}
    .toolbar input:focus,.toolbar select:focus{border-color:#10b981}

    /* Buttons */
    .btn{padding:9px 20px;border-radius:12px;border:none;font-weight:600;cursor:pointer;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:transform .15s,box-shadow .15s}
    .btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.12)}
    .btn-primary{background:linear-gradient(135deg,#10b981,#059669);color:#fff}
    .btn-secondary{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0}
    .btn-danger{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff}
    .btn-info{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff}
    .btn-sm{padding:5px 12px;font-size:.78rem;border-radius:9px}

    /* Table */
    .table-wrap{overflow-x:auto}
    .table-pro{width:100%;border-collapse:separate;border-spacing:0 5px;min-width:900px}
    .table-pro th{text-align:left;padding:11px 14px;color:#64748b;font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
    .table-pro td{padding:12px 14px;background:rgba(255,255,255,.75);font-size:.85rem;color:#1e293b;border-bottom:1px solid #f1f5f9;white-space:nowrap}
    .table-pro tbody tr{transition:background .15s}
    .table-pro tbody tr:hover td{background:rgba(16,185,129,.04)}
    .table-pro tr td:first-child{border-radius:10px 0 0 10px}
    .table-pro tr td:last-child{border-radius:0 10px 10px 0}

    /* Badges */
    .badge{display:inline-block;padding:3px 11px;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase}
    .badge-aktif{background:#d1fae5;color:#059669}
    .badge-pasif{background:#f1f5f9;color:#64748b}

    .action-group{display:flex;gap:5px;align-items:center;justify-content:center}
    .pagination-wrap{display:flex;justify-content:center;margin-top:18px}
    .alert{padding:13px 18px;border-radius:13px;margin-bottom:18px;font-weight:500;display:flex;align-items:center;gap:9px}
    .alert-success{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .ml-auto{margin-left:auto}
</style>

<div class="page-hero">
    <div class="hero-container">
        <div class="hero-title-group">
            <h1>🪣 Kuyu Envanteri</h1>
            <p class="hero-subtitle">Su kuyuları kayıt, takip ve izleme modülü — <strong style="color:#6ee7b7">{{ number_format($toplamKuyu) }}</strong> kuyu</p>
        </div>
        @can('manage_kuyu_envanteri')
        <a href="{{ route('kuyu-envanteri.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Kuyu Ekle
        </a>
        @endcan
    </div>
</div>

<div class="main-container">

    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- İstatistik Kartları --}}
    <div class="stat-grid">
        <a href="{{ route('kuyu-envanteri.index', ['filter' => 'all']) }}" class="stat-card {{ $aktifFiltre === 'all' ? 'active' : '' }}">
            <div class="stat-icon green"><i class="fas fa-water"></i></div>
            <div><div class="stat-value">{{ number_format($toplamKuyu) }}</div><div class="stat-label">Toplam Kuyu</div></div>
        </a>
        <a href="{{ route('kuyu-envanteri.index', ['filter' => 'cbssiz']) }}" class="stat-card {{ $aktifFiltre === 'cbssiz' ? 'active' : '' }}">
            <div class="stat-icon orange"><i class="fas fa-map-marker-alt"></i></div>
            <div><div class="stat-value">{{ number_format($cbssizKuyu) }}</div><div class="stat-label">CBS Bilgisi Olmayan</div></div>
        </a>
        <a href="{{ route('kuyu-envanteri.index', ['filter' => 'kuyusuz']) }}" class="stat-card {{ $aktifFiltre === 'kuyusuz' ? 'active' : '' }}">
            <div class="stat-icon blue"><i class="fas fa-hashtag"></i></div>
            <div><div class="stat-value">{{ number_format($kuyusuzKuyu) }}</div><div class="stat-label">Kuyu No Olmayan</div></div>
        </a>
        <a href="{{ route('kuyu-envanteri.index', ['filter' => 'pasif']) }}" class="stat-card {{ $aktifFiltre === 'pasif' ? 'active' : '' }}">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div><div class="stat-value">{{ number_format($pasifKuyu) }}</div><div class="stat-label">Pasif Kuyu</div></div>
        </a>
        <a href="{{ route('kuyu-envanteri.index', ['filter' => 'abonesiz']) }}" class="stat-card {{ $aktifFiltre === 'abonesiz' ? 'active' : '' }}">
            <div class="stat-icon orange"><i class="fas fa-link"></i></div>
            <div><div class="stat-value">{{ number_format($abonesizKuyu) }}</div><div class="stat-label">Abonesiz Kuyu</div></div>
        </a>
    </div>

    {{-- Tablo --}}
    <div class="glass-card">
        <form class="toolbar" method="GET">
            <input type="text" name="arama" placeholder="Kuyu no / adres / motor / pompa…" value="{{ request('arama') }}" style="min-width:240px">
            <select name="ilce" style="min-width:160px">
                <option value="">Tüm İlçeler</option>
                @foreach($ilceler as $ilce)
                    <option value="{{ $ilce }}" {{ request('ilce') == $ilce ? 'selected' : '' }}>{{ $ilce }}</option>
                @endforeach
            </select>
            <select name="durum">
                <option value="">Tüm Durumlar</option>
                <option value="aktif" {{ request('durum') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="pasif" {{ request('durum') == 'pasif' ? 'selected' : '' }}>Pasif</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrele</button>
            @if(request()->anyFilled(['arama','ilce','durum']))
                <a href="{{ route('kuyu-envanteri.index') }}" class="btn btn-secondary">Temizle</a>
            @endif
            <div class="ml-auto" style="font-size:.82rem;color:#64748b;padding-top:4px">
                <i class="fas fa-list"></i> {{ $kuyular->total() }} sonuç
            </div>
        </form>

        <div class="table-wrap">
            <table class="table-pro">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kuyu No</th>
                        <th>Abone No</th>
                        <th>İlçe</th>
                        <th>Adres</th>
                        <th>Durum</th>
                        <th style="text-align:center">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kuyular as $kuyu)
                    <tr>
                        <td style="color:#94a3b8;font-size:.78rem">{{ $loop->iteration + ($kuyular->currentPage()-1) * $kuyular->perPage() }}</td>
                        <td><strong>{{ $kuyu->kuyu_no ?? '-' }}</strong></td>
                        <td><span style="font-weight:600; color:#3b82f6;">{{ $kuyu->abone_no ?? $kuyu->arizaKaydi->abone_no ?? '-' }}</span></td>
                        <td>{{ $kuyu->ilce ?? '-' }}</td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis" title="{{ $kuyu->adres }}">{{ $kuyu->adres ?? '-' }}</td>
                        <td><span class="badge badge-{{ $kuyu->durum }}">{{ $kuyu->durum_label }}</span></td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('kuyu-envanteri.show', $kuyu) }}" class="btn btn-info btn-sm" title="Detay"><i class="fas fa-eye"></i></a>
                                @can('manage_kuyu_envanteri')
                                <a href="{{ route('kuyu-envanteri.edit', $kuyu) }}" class="btn btn-primary btn-sm" title="Düzenle"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('kuyu-envanteri.destroy', $kuyu) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('Bu kuyuyu silmek istediğinize emin misiniz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Sil"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:50px;color:#94a3b8">
                            <i class="fas fa-water" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.3"></i>
                            Kayıt bulunamadı.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $kuyular->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
