@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Regions */
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
    }

    .pg-premium {
        background-color: var(--bg-main) !important;
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative;
        padding: 4rem 2rem 8rem 2rem;
        margin-top: -20px;
        color: #fff;
        overflow: hidden;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .page-hero::before {
        content: ''; position: absolute; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%);
        top: -150px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }

    /* Main Container */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }

    .card-title-pro { font-size: 1.2rem; font-weight: 800; color: var(--text-slate-900); display: flex; align-items: center; gap: 12px; margin-bottom: 25px; }
    .card-title-pro i { padding: 10px; background: #f1f5f9; border-radius: 12px; color: #3b82f6; }

    /* Filter Input */
    .filter-wrapper { display: flex; gap: 12px; align-items: flex-end; }
    .input-grp-pro { flex: 1; }
    .input-grp-pro label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-slate-500); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-control-pro {
        width: 100%; padding: 12px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        font-size: 0.95rem; font-weight: 500; color: var(--text-slate-900); transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }

    /* Buttons */
    .btn-pro {
        padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4); }
    
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }

    /* Table Design */
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-pro th { color: #94a3b8; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 20px; text-align: left; border: none; }
    .table-pro td { background: #fff; padding: 16px 20px; vertical-align: middle; border: none; transition: all 0.2s ease; }
    .table-pro tr td:first-child { border-top-left-radius: 18px; border-bottom-left-radius: 18px; }
    .table-pro tr td:last-child { border-top-right-radius: 18px; border-bottom-right-radius: 18px; }
    .table-pro tr:hover td { background: #f8fafc; transform: scale(1.005); box-shadow: 0 5px 15px rgba(0,0,0,0.02); }

    .badge-code { background: #eff6ff; color: #2563eb; padding: 6px 14px; border-radius: 10px; font-weight: 800; font-size: 0.75rem; }
    
    .action-btn { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fff7ed; color: #ea580c; border: 1px solid transparent; transition: all 0.2s; cursor: pointer; }
    .action-btn:hover { background: #ffedd5; border-color: #fdba74; transform: translateY(-2px); }

    /* Modal Styling */
    .modal-pro .modal-content { border-radius: 28px; border: none; overflow: hidden; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .modal-pro .modal-header { padding: 25px 30px; border-bottom: 1px solid #f1f5f9; background: #fff; }
    .modal-pro .modal-title { font-weight: 800; color: var(--text-slate-900); font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
    .modal-pro .modal-body { padding: 30px; }
    .modal-pro .modal-footer { padding: 20px 30px; background: #f8fafc; border-top: 1px solid #f1f5f9; }

    .sempty-pro { text-align: center; padding: 60px 40px; color: #94a3b8; }
    .sempty-pro i { font-size: 3rem; margin-bottom: 15px; opacity: 0.3; display: block; }
</style>

<div class="pg-premium p-0">
    <!-- HERO SECTION -->
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Bölge Yönetimi</h1>
                <p class="hero-subtitle">Kayıtlı su dağıtım bölgelerini ve kodlarını yönetin.</p>
            </div>
            <div>
                <span class="btn-pro btn-outline-pro" style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-map-marked-alt"></i> Toplam {{ $bolgeler->total() }} Bölge
                </span>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- FILTER CARD -->
        <div class="glass-card">
            <h5 class="card-title-pro"><i class="fas fa-search"></i> Bölge Arama</h5>
            <form action="{{ route('bolgeler.index') }}" method="GET">
                <div class="filter-wrapper">
                    <div class="input-grp-pro">
                        <label>Bölge Adı veya Kodu</label>
                        <input type="text" name="search" class="form-control-pro" value="{{ request('search') }}" placeholder="Aramak istediğiniz bölgeyi yazın...">
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('bolgeler.index') }}" class="btn-pro btn-outline-pro">Sıfırla</a>
                        <button type="submit" class="btn-pro btn-primary-pro">
                            <i class="fas fa-filter"></i> Bölgeleri Filtrele
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLE CARD -->
        <div class="glass-card">
            <h5 class="card-title-pro"><i class="fas fa-list-ul"></i> Tüm Bölge Kayıtları</h5>
            <div class="table-responsive">
                <table class="table-pro">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th style="width: 150px;">Bölge Kodu</th>
                            <th>Bölge Adı</th>
                            <th style="width: 100px; text-align: right;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bolgeler as $bolge)
                            @php $iter = $loop->iteration + ($bolgeler->currentPage()-1)*$bolgeler->perPage(); @endphp
                            <tr>
                                <td style="font-weight: 700; color: #94a3b8;">{{ str_pad($iter, 2, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <span class="badge-code">{{ $bolge->bolge_kodu }}</span>
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:10px; height:10px; border-radius:50%; background:hsl({{ ($loop->iteration * 137) % 360 }}, 70%, 50%);"></div>
                                        <span style="font-weight:700; color:var(--text-slate-900);">{{ $bolge->bolge_adi }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        <button class="action-btn" title="Düzenle" 
                                                onclick="openEditModal({{ $bolge->id }}, '{{ $bolge->bolge_kodu }}', '{{ $bolge->bolge_adi }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="sempty-pro">
                                        <i class="fas fa-map"></i>
                                        <p>Kayıtlı bölge bulunamadı veya arama sonucu boş.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bolgeler->hasPages())
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    {{ $bolgeler->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- EDIT MODAL (ATLANTIS STYLE ADAPTED TO PREMIUM) -->
<div class="modal fade modal-pro" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt" style="color: #ea580c;"></i> 
                    Bölge Düzenleme
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group-pro" style="margin-bottom: 20px;">
                        <label style="display:block; font-size:.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Bölge Kodu (Sabit)</label>
                        <input type="text" id="m_kodu" class="form-control-pro" readonly style="background: #f8fafc; border-color: #f1f5f9; color: #94a3b8; cursor: not-allowed;">
                    </div>
                    <div class="form-group-pro">
                        <label style="display:block; font-size:.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Yeni Bölge Adı</label>
                        <input type="text" name="bolge_adi" id="m_adi" required class="form-control-pro" placeholder="Örn: Merkez Bölgesi">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-pro btn-outline-pro" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn-pro btn-primary-pro">
                        <i class="fas fa-check-circle"></i> Bilgileri Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, kodu, adi) {
    $('#editForm').attr('action', '/bolgeler/' + id);
    $('#m_kodu').val(kodu);
    $('#m_adi').val(adi);
    $('#editModal').modal('show');
}
</script>

@endsection