@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
        --bg-main: #f4f6f9;
        --card-bg: rgba(255, 255, 255, 0.95);
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
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .table-pro th { color: #94a3b8; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 15px 20px; text-align: left; border: none; }
    .table-pro td { background: #fff; padding: 16px 20px; vertical-align: middle; border: none; transition: all 0.2s ease; }
    .table-pro tr td:first-child { border-top-left-radius: 18px; border-bottom-left-radius: 18px; }
    .table-pro tr td:last-child { border-top-right-radius: 18px; border-bottom-right-radius: 18px; }
    .table-pro tr:hover td { background: #f8fafc; transform: scale(1.002); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02); }
    .badge-pro { padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; }
    .badge-purple { background: #f3e8ff; color: #7c3aed; }
    .btn-pro { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important; }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(139, 92, 246, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }
    .action-btn-text { height: 38px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; padding: 0 16px; font-weight: 700; font-size: .8rem; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none !important; }
    .ab-purple { background: #f3e8ff; color: #7c3aed; }
    .ab-purple:hover { background: #7c3aed; color: #fff; transform: translateY(-2px); }
    .ab-green { background: #f0fdf4; color: #16a34a; }
    .ab-green:hover { background: #16a34a; color: #fff; transform: translateY(-2px); }
    .ab-gray { background: #f1f5f9; color: #475569; }
    .ab-gray:hover { background: #475569; color: #fff; transform: translateY(-2px); }
    .tab-count-pro { background: rgba(0, 0, 0, 0.05); padding: 2px 8px; border-radius: 8px; font-size: 0.75rem; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Arıza Türleri</h1>
                <p class="hero-subtitle">Sistemdeki arıza türlerini görüntüleyin ve yönetin.</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title-pro mb-0">
                    <i class="fas fa-tools" style="color:#8b5cf6;"></i>
                    Arıza Türleri
                    <span class="tab-count-pro" style="margin-left:8px;">{{ $turler->total() }}</span>
                </h5>
                <a href="{{ route('tesis-bilgi-sistemi.ariza-turleri.create') }}" class="btn-pro btn-primary-pro">
                    <i class="fas fa-plus"></i> Yeni Tür
                </a>
            </div>

            <div class="table-responsive">
                <table class="table-pro">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Arıza Türü</th>
                            <th style="width: 120px; text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turler as $t)
                        <tr id="tur-row-{{ $t->id }}">
                            <td style="font-weight: 700; color: #94a3b8;">{{ $loop->iteration }}</td>
                            <td><span class="badge-pro badge-purple">{{ $t->ad }}</span></td>
                            <td style="text-align: right;">
                                <div class="d-flex justify-content-end" style="gap:6px;">
                                    <a href="{{ route('tesis-bilgi-sistemi.ariza-turleri.edit', $t->id) }}" class="action-btn-text ab-purple" style="font-size:0.75rem; padding:0 12px; text-decoration:none;">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button" class="action-btn-text ab-gray" style="font-size:0.75rem; padding:0 12px; color:#dc2626;" onclick="confirmDelete({{ $t->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center" style="padding: 60px;">
                                <i class="fas fa-folder-open mb-3" style="font-size: 3rem; color: #e2e8f0; display: block;"></i>
                                <p style="color: #94a3b8; font-weight: 600;">Henüz arıza türü eklenmemiş.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($turler->hasPages())
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                {{ $turler->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu arıza türünü silmek istediğinizden emin misiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/tesis-bilgi-sistemi/ariza-turleri/' + id,
                    type: 'POST',
                    data: { _method: 'DELETE' },
                    success: function() {
                        var row = $('#tur-row-' + id);
                        row.fadeOut(300, function() { row.remove(); });
                        Swal.fire({
                            icon: 'success',
                            title: 'Silindi!',
                            text: 'Arıza türü başarıyla silindi.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Silme işlemi sırasında bir hata oluştu.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
